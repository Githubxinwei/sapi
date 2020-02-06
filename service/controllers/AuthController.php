<?php
namespace service\controllers;

use common\models\Employee;
use service\base\BaseController;
use service\controllers\WorkController;
use Yii;

class AuthController extends BaseController
{
    /**
     * @api               {GET}           /service/auth/lists                                 菜单列表
     * @apiVersion        1.0.0
     * @apiName           菜单列表
     * @apiGroup          auth
     * @apiDescription    菜单列表 - 登录用户可操作的菜单列表
     * <br />
     * <span><strong>作    者：</strong></span>王海洋<br/>
     * <span><strong>邮    箱：</strong></span>wanghaiyang@xingfufit.com
     * <span><strong>创建时间：</strong></span>2018-04-19
     * @apiSampleRequest                  http://qaserviceapi.xingfufit.cn/service/auth/lists
     * @apiSuccess        (返回值)           {json}                                      data
     * @apiSuccessExample {json}返回值详情（成功）
     *
{
    "code": 1,
    "status": "success",
    "message": "",
    "data": {
        "common": {
            "common": {
                "title": "常用模块",
                "015": "上下课打卡",
                "017": "我的会员",
                "014": "卖课排行榜",
                "025": "管理预约表",
                "026": "管理排班表",
                "020": "分配教练"
            }
        },
        "list": [
            {
                "id": "104",
                "name": "私教",
                "e_name": "coach",
                "children": [
                    {
                        "id": "105",
                        "name": "上下课打卡",
                        "e_name": "punchcard",
                        "url": "015"
                    },
                    {
                        "id": "107",
                        "name": "预约表",
                        "e_name": "yyb",
                        "url": "023"
                    }
                ]
            },
            {
                "id": "108",
                "name": "管理",
                "e_name": "manage",
                "children": [
                    {
                        "id": "110",
                        "name": "分配教练",
                        "e_name": "fenpei",
                        "url": "020"
                    }
                ]
            }
        ]
    }
}
     * @apiSuccessExample {json}返回值详情（失败）
{
    "code": 0,
    "status": "error",
    "message": "权限为空"
}
     */
    public function actionLists()
    {
        $moduleArr = self::getAuthList($this->employeeId);
        if (count($moduleArr) == 0) {
            return $this->error('权限为空');
        }

        // 获取当前用户的常用配置菜单
        $work = new WorkController($this->id, $this->module);
        $common = $work->getCommon(
            $this->user->employee->organization->name,
            $this->user->isManager,
            $this->employeeId
        );

        // 过滤用户常用菜单中无效的
        $moduleUrlArr = array_filter(array_column($moduleArr, 'url'));
        foreach ($common['common'] as $k => $v) {
            if ($k == 'title') {
                continue;
            }
            if (!in_array($k, $moduleUrlArr)) {
                unset($common['common'][$k]);
            }
        }

        // 过滤手机端菜单中"消息"的菜单，并格式化返回
        self::getTree($moduleArr, $tree);
        $return = array();
        foreach ($tree as $k1 => $v1) {
            if ($v1['name'] == '消息') {
                continue;
            }
            $temp = $v1;
            unset($temp['pid'], $temp['url'], $temp['children']);
            foreach ($v1['children'] as $k2 => $v2) {
                unset($v2['pid'], $v2['children']);
                $v1['children'][$k2] = $v2;
            }
            $temp['children'] = array_values($v1['children']);
            $return[] = $temp;
        }
        return $this->success(['common' => $common, 'list' => $return]);
    }

    public function getTree($list, &$tree, $pid = 0)
    {
        foreach ($list as $k => $v) {
            if ($v['pid'] == $pid) {
                $tree[$v['id']] = $v;
                self::getTree($list, $tree[$v['id']]['children'], $v['id']);
            }
        }
    }

    public function getAuthList($employeeId)
    {
        // 获取当前员工角色对应的权限
        $result = Employee::find()
            ->alias('e')
            ->joinWith(['admin a' => function($query) {
                $query->joinWith('auth au');
                $query->joinWith(['role r'], false);
            }])
            ->where(['e.id' => $employeeId, 'e.status' => 1])
            ->andWhere(['r.status' => 1])
            ->asArray()
            ->one();
        if (!isset($result['id'])) {
            return [];
        }

        $module_id = isset($result['admin']['auth']['id']) ? $result['admin']['auth']['module_id'] : '';
        $module_id = json_decode($module_id, true);

        // 获取所有的手机端可用菜单列表
        $moduleRs = \common\models\base\Module::find()
            ->select('id, name, e_name, pid, url')
            ->where(['type' => 2])
            ->asArray()
            ->all();
        $moduleIds = array_column($moduleRs, 'id');
        $moduleIds = array_intersect($module_id, $moduleIds);
        if (count($moduleIds) == 0) {
            return [];
        }

        // 根据用户权限和手机菜单，获取用户的可用菜单
        $moduleArr = array();
        foreach ($moduleRs as $v) {
            if (in_array($v['id'], $moduleIds)) {
                $moduleArr[] = $v;
            }
        }
        return $moduleArr;
    }
}
